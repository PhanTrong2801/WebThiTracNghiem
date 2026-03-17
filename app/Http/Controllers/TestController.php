<?php

namespace App\Http\Controllers;

use App\Models\CauHoiModel;
use App\Models\CauTraLoiModel;
use App\Models\ChiTietDeThiModel;
use App\Models\ChiTietKetQuaModel;
use App\Models\DeThiModel;
use App\Models\KetQuaModel;
use App\Models\ChuongModel;
use App\Models\MonHocModel;
use App\Models\NhomModel;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $search = $request->query('search');

        $query = DeThiModel::query()
            ->with(['monhoc'])
            ->when($search, function($q) use ($search) {
                $q->where(function($sq) use ($search) {
                    $sq->where('tende', 'like', "%{$search}%")
                       ->orWhere('monthi', 'like', "%{$search}%")
                       ->orWhereHas('monhoc', function($mq) use ($search) {
                           $mq->where('tenmonhoc', 'like', "%{$search}%");
                       });
                });
            });

        // Nếu là giảng viên: 
        if ($user) {
            $query->where('nguoitao', $user->id);
        }

        $danhSachDeThi = $query->orderByDesc('made')->paginate(10)->withQueryString();

        return Inertia::render('Tests/Index', [
            'danhSachDeThi' => $danhSachDeThi,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(Request $request)
    {
        $userId = $request->user()->id;

        return Inertia::render('Tests/Form', [
            'action' => 'create',
            // Chỉ hiện môn được phân công giảng dạy (giống project kia)
            'danhSachMonHoc' => MonHocModel::active()
                ->whereIn('mamonhoc', function ($q) use ($userId) {
                    $q->select('mamonhoc')->from('phancong')->where('manguoidung', $userId);
                })
                ->orderBy('tenmonhoc')
                ->get(['mamonhoc', 'tenmonhoc']),
            // Danh sách nhóm của giảng viên để giao đề
            'danhSachNhom' => NhomModel::active()
                ->where('giangvien', $userId)
                ->orderByDesc('namhoc')
                ->orderByDesc('hocky')
                ->orderByDesc('manhom')
                ->get(['manhom', 'tennhom', 'namhoc', 'hocky', 'mamonhoc', 'hienthi']),
            'test' => null,
            'selectedNhomIds' => [],
            'selectedChuongIds' => [],
        ]);
    }

    public function edit(Request $request, int $made)
    {
        $userId = $request->user()->id;
        $test = DeThiModel::query()
            ->withCount('cauhoi')
            ->findOrFail($made);

        if ((string) $test->nguoitao !== (string) $userId) {
            abort(403);
        }

        $selectedNhomIds = DB::table('giaodethi')->where('made', $made)->pluck('manhom')->toArray();
        $selectedChuongIds = DB::table('dethitudong')->where('made', $made)->pluck('machuong')->toArray();

        return Inertia::render('Tests/Form', [
            'action' => 'update',
            'danhSachMonHoc' => MonHocModel::active()
                ->whereIn('mamonhoc', function ($q) use ($userId) {
                    $q->select('mamonhoc')->from('phancong')->where('manguoidung', $userId);
                })
                ->orderBy('tenmonhoc')
                ->get(['mamonhoc', 'tenmonhoc']),
            'danhSachNhom' => NhomModel::active()
                ->where('giangvien', $userId)
                ->orderByDesc('namhoc')
                ->orderByDesc('hocky')
                ->orderByDesc('manhom')
                ->get(['manhom', 'tennhom', 'namhoc', 'hocky', 'mamonhoc', 'hienthi']),
            'test' => $test,
            'selectedNhomIds' => $selectedNhomIds,
            'selectedChuongIds' => $selectedChuongIds,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateTest($request);
        $data['nguoitao'] = $request->user()->id;
        $data['thoigiantao'] = now();

        $nhomIds = $this->validateNhomIds($request);
        $chuongIds = $this->validateChuongIds($request);

        $test = null;
        DB::transaction(function () use ($data, $nhomIds, $chuongIds, &$test) {
            $test = DeThiModel::create($data);
            $this->syncGiaoDeThi($test->made, $nhomIds);
            $this->syncDeThiTuDong($test->made, $chuongIds);

            if ((int) $test->loaide === 1) {
                $this->generateAutoQuestionsOrFail($test, $chuongIds);
            }
        });

        $msg = ((int) $test->loaide === 1)
            ? 'Tạo đề thi tự động thành công. Hệ thống đã sinh câu hỏi theo chương/độ khó.'
            : 'Tạo đề thi thành công. Hãy chọn câu hỏi cho đề.';

        return redirect()->route('tests.select', ['made' => $test->made])->with('success', $msg);
    }

    public function update(Request $request, int $made)
    {
        $test = DeThiModel::findOrFail($made);

        // Chỉ cho phép người tạo sửa
        if ($test->nguoitao !== $request->user()->id) {
            abort(403);
        }

        $data = $this->validateTest($request);
        $nhomIds = $this->validateNhomIds($request);
        $chuongIds = $this->validateChuongIds($request);

        DB::transaction(function () use ($test, $data, $nhomIds, $chuongIds) {
            $test->update($data);
            $this->syncGiaoDeThi($test->made, $nhomIds);
            $this->syncDeThiTuDong($test->made, $chuongIds);

            if ((int) $test->loaide === 1) {
                $this->generateAutoQuestionsOrFail($test->fresh(), $chuongIds);
            }
        });

        return redirect()->route('tests.select', ['made' => $test->made])
            ->with('success', 'Cập nhật đề thi thành công.');
    }

    public function select(Request $request, int $made)
    {
        $test = DeThiModel::query()->with(['monhoc'])->findOrFail($made);

        if ($test->nguoitao !== $request->user()->id) {
            abort(403);
        }

        $search = $request->query('search');
        $dokho = $request->query('dokho');
        $machuong = $request->query('machuong');

        $chapters = ChuongModel::query()
            ->active()
            ->where('mamonhoc', $test->monthi)
            ->orderBy('machuong')
            ->get(['machuong', 'tenchuong']);

        $questions = CauHoiModel::query()
            ->with(['cautraloi'])
            ->active()
            ->where('mamonhoc', $test->monthi)
            ->when($machuong, fn ($q) => $q->where('machuong', $machuong))
            ->when($search, fn ($q) => $q->where('noidung', 'like', "%{$search}%"))
            ->when($dokho, fn ($q) => $q->where('dokho', $dokho))
            ->orderByDesc('macauhoi')
            ->paginate(10)
            ->withQueryString();

        $selectedIds = ChiTietDeThiModel::query()
            ->where('made', $made)
            ->orderBy('thutu')
            ->pluck('macauhoi');

        return Inertia::render('Tests/SelectQuestions', [
            'test' => $test,
            'questions' => $questions,
            'selectedIds' => $selectedIds,
            'chapters' => $chapters,
            'filters' => [
                'search' => $search,
                'dokho' => $dokho,
                'machuong' => $machuong,
            ],
        ]);
    }

    public function saveSelectedQuestions(Request $request, int $made)
    {
        $test = DeThiModel::findOrFail($made);
        if ($test->nguoitao !== $request->user()->id) {
            abort(403);
        }

        $ids = $request->input('selectedIds', []);
        if (!is_array($ids)) $ids = [];

        DB::transaction(function () use ($made, $ids) {
            ChiTietDeThiModel::where('made', $made)->delete();

            $thutu = 1;
            foreach ($ids as $qid) {
                if (!filter_var($qid, FILTER_VALIDATE_INT)) continue;
                ChiTietDeThiModel::create([
                    'made' => $made,
                    'macauhoi' => (int) $qid,
                    'thutu' => $thutu++,
                ]);
            }
        });

        return back()->with('success', 'Đã lưu danh sách câu hỏi cho đề.');
    }

    public function schedule(Request $request)
    {
        $user = $request->user();
        $search = $request->query('search');

        // Lấy danh sách nhóm user tham gia
        $nhomIds = DB::table('chitietnhom')
            ->where('manguoidung', $user->id)
            ->pluck('manhom');

        // Lấy danh sách mã môn học từ các nhóm user tham gia
        $mamonhocIds = DB::table('nhom')
            ->whereIn('manhom', $nhomIds)
            ->pluck('mamonhoc');

        // Lấy IDs các đề đã làm (có điểm)
        $completedIds = DB::table('ketqua')
            ->where('manguoidung', $user->id)
            ->whereNotNull('diemthi')
            ->pluck('made');

        // Logic tìm kiếm chung
        $applySearch = function($q) use ($search) {
            $q->where(function($sq) use ($search) {
                $sq->where('tende', 'like', "%{$search}%")
                   ->orWhere('monthi', 'like', "%{$search}%")
                   ->orWhereHas('monhoc', function($mq) use ($search) {
                       $mq->where('tenmonhoc', 'like', "%{$search}%");
                   });
            });
        };

        // 1. Phân trang Đề thi sắp tới
        $assignedExams = DeThiModel::query()
            ->with('monhoc')
            ->whereIn('monthi', $mamonhocIds)
            ->whereNotIn('made', $completedIds)
            ->when($search, $applySearch)
            ->orderByDesc('made')
            ->paginate(7, ['*'], 'ap')
            ->withQueryString();

        // 2. Phân trang Đề thi đã hoàn thành
        $completedExams = DeThiModel::query()
            ->with('monhoc')
            ->whereIn('made', $completedIds)
            ->when($search, $applySearch)
            ->orderByDesc('made')
            ->paginate(7, ['*'], 'cp')
            ->withQueryString();

        return Inertia::render('Tests/Schedule', [
            'assignedExams' => $assignedExams,
            'completedExams' => $completedExams,
            'filters' => ['search' => $search],
        ]);
    }

    public function start(Request $request, int $made)
    {
        $test = DeThiModel::query()->with(['monhoc'])->findOrFail($made);

        // Check được phép (đã giao cho nhóm)
        $allowed = $this->checkStudentAllowed($request->user()->id, $made);
        if (!$allowed) abort(403);

        $kq = KetQuaModel::query()
            ->where('made', $made)
            ->where('manguoidung', $request->user()->id)
            ->first();

        return Inertia::render('Tests/Start', [
            'test' => $test,
            'ketqua' => $kq,
            'now' => now()->toDateTimeString(),
        ]);
    }

    public function take(Request $request, int $made)
    {
        $test = DeThiModel::query()
            ->with(['cauhoi.cautraloi'])
            ->findOrFail($made);

        $allowed = $this->checkStudentAllowed($request->user()->id, $made);
        if (!$allowed) abort(403);

        $now = Carbon::now();
        if ($test->thoigianbatdau && $now->lt(Carbon::parse($test->thoigianbatdau))) {
            return redirect()->route('tests.start', ['made' => $made])->with('error', 'Chưa đến thời gian bắt đầu.');
        }
        if ($test->thoigianketthuc && $now->gt(Carbon::parse($test->thoigianketthuc))) {
            return redirect()->route('tests.start', ['made' => $made])->with('error', 'Đã hết thời gian làm bài.');
        }

        $kq = KetQuaModel::firstOrCreate(
            ['made' => $made, 'manguoidung' => $request->user()->id],
            ['thoigianvaothi' => now(), 'solanchuyentab' => 0]
        );

        // Nếu đã có điểm thì không cho vào lại
        if ($kq->diemthi !== null) {
            return redirect()->route('tests.start', ['made' => $made]);
        }

        $questions = $test->cauhoi;

        if ($test->troncauhoi) {
            $questions = $questions->shuffle()->values();
        }

        // Trộn đáp án theo câu hỏi nếu bật
        $questions = $questions->map(function ($q) use ($test) {
            $answers = $q->cautraloi;
            if ($test->trondapan) {
                $answers = $answers->shuffle()->values();
            }
            $q->setRelation('cautraloi', $answers);
            return $q;
        });

        return Inertia::render('Tests/Take', [
            'test' => $test,
            'ketqua' => $kq,
            'questions' => $questions,
        ]);
    }

    public function submit(Request $request, int $made)
    {
        $test = DeThiModel::query()->with(['cauhoi.cautraloi'])->findOrFail($made);

        $allowed = $this->checkStudentAllowed($request->user()->id, $made);
        if (!$allowed) abort(403);

        $kq = KetQuaModel::query()
            ->where('made', $made)
            ->where('manguoidung', $request->user()->id)
            ->firstOrFail();

        if ($kq->diemthi !== null) {
            return redirect()->route('tests.start', ['made' => $made])->with('success', 'Bạn đã nộp bài trước đó.');
        }

        $answers = $request->input('answers', []); // { macauhoi: macautl }
        if (!is_array($answers)) $answers = [];

        $questionIds = $test->cauhoi->pluck('macauhoi')->all();

        $correct = 0;
        DB::transaction(function () use ($kq, $answers, $test, $questionIds, &$correct) {
            ChiTietKetQuaModel::where('makq', $kq->makq)->delete();

            $total = count($questionIds);
            foreach ($questionIds as $qid) {
                $chosen = $answers[$qid] ?? null;
                $chosenId = filter_var($chosen, FILTER_VALIDATE_INT) ? (int) $chosen : null;

                ChiTietKetQuaModel::create([
                    'makq' => $kq->makq,
                    'macauhoi' => (int) $qid,
                    'dapanchon' => $chosenId,
                ]);

                if ($chosenId) {
                    $isCorrect = CauTraLoiModel::query()
                        ->where('macautl', $chosenId)
                        ->where('macauhoi', $qid)
                        ->where('ladapan', 1)
                        ->exists();
                    if ($isCorrect) $correct++;
                }
            }

            $score = $total > 0 ? round(($correct / $total) * 10, 2) : 0;
            $kq->update([
                'diemthi' => $score,
                'socaudung' => $correct,
            ]);
        });

        return redirect()->route('tests.start', ['made' => $made])->with('success', 'Nộp bài thành công.');
    }

    public function results(Request $request, int $made)
    {
        $test = DeThiModel::query()->with(['monhoc'])->findOrFail($made);
        if ($test->nguoitao !== $request->user()->id) {
            abort(403);
        }

        $ketqua = KetQuaModel::query()
            ->with(['user'])
            ->where('made', $made)
            ->orderByDesc('makq')
            ->paginate(10);

        return Inertia::render('Tests/Results', [
            'test' => $test,
            'danhSachKetQua' => $ketqua,
        ]);
    }

    /**
     * Trang chi tiết đề thi (GV): Danh sách + Thống kê + xem bài làm
     */
    public function detail(Request $request, int $made)
    {
        $test = DeThiModel::query()->with(['monhoc'])->findOrFail($made);
        if ((string) $test->nguoitao !== (string) $request->user()->id) {
            abort(403);
        }

        $groups = DB::table('giaodethi as gdt')
            ->join('nhom as n', 'n.manhom', '=', 'gdt.manhom')
            ->where('gdt.made', $made)
            ->select(['n.manhom', 'n.tennhom', 'n.namhoc', 'n.hocky'])
            ->orderByDesc('n.namhoc')
            ->orderByDesc('n.hocky')
            ->orderByDesc('n.manhom')
            ->get();

        $defaultGroup = (int) ($request->query('manhom') ?? ($groups[0]->manhom ?? 0));

        return Inertia::render('Tests/Detail', [
            'test' => $test,
            'groups' => $groups,
            'defaultGroup' => $defaultGroup,
        ]);
    }

    /**
     * API thống kê 
     * Query param: manhom (0 = tất cả nhóm của đề)
     */
    public function getStatistical(Request $request, int $made)
    {
        $test = DeThiModel::findOrFail($made);
        if ((string) $test->nguoitao !== (string) $request->user()->id) {
            abort(403);
        }

        $manhom = (int) ($request->query('manhom') ?? 0);

        $q = DB::table('chitietnhom as ctn')
            ->join('giaodethi as gdt', 'ctn.manhom', '=', 'gdt.manhom')
            ->leftJoin('ketqua as kq', function ($join) use ($made) {
                $join->on('kq.manguoidung', '=', 'ctn.manguoidung')
                    ->where('kq.made', '=', $made);
            })
            ->where('gdt.made', $made);

        if ($manhom !== 0) {
            $q->where('ctn.manhom', $manhom);
        }

        $rows = $q->select([
            'ctn.manguoidung',
            'kq.manguoidung as mandkq',
            'kq.makq',
            'kq.diemthi',
        ])->get();

        $bins = array_fill(0, 10, 0);
        $tongdiem = 0;
        $soluong = 0;
        $max = 0;
        $chuanop = 0;
        $khongthi = 0;

        foreach ($rows as $r) {
            if ($r->diemthi !== null) {
                $d = (float) $r->diemthi;
                $tongdiem += $d;
                $soluong++;
                $index = (int) ceil($d) > 0 ? ((int) ceil($d) - 1) : 0;
                if ($index < 0) $index = 0;
                if ($index > 9) $index = 9;
                $bins[$index]++;
                if ($d > $max) $max = $d;
            } else {
                if ($r->mandkq !== null) $chuanop++;
                else $khongthi++;
            }
        }

        return response()->json([
            'diem_trung_binh' => $soluong !== 0 ? round($tongdiem / $soluong, 2) : 0,
            'da_nop_bai' => $soluong,
            'chua_nop_bai' => $chuanop,
            'khong_thi' => $khongthi,
            'diem_cao_nhat' => $max,
            'thong_ke_diem' => $bins,
        ]);
    }

    /**
     * API danh sách điểm theo nhóm 
     * Query param: manhom (bắt buộc)
     */
    public function getScores(Request $request, int $made)
    {
        $test = DeThiModel::findOrFail($made);
        if ((string) $test->nguoitao !== (string) $request->user()->id) {
            abort(403);
        }

        $manhom = (int) ($request->query('manhom') ?? 0);
        if ($manhom <= 0) {
            return response()->json([]);
        }

        $rows = DB::table('giaodethi as gdt')
            ->join('chitietnhom as ctn', 'gdt.manhom', '=', 'ctn.manhom')
            ->join('users as u', 'u.id', '=', 'ctn.manguoidung')
            ->leftJoin('ketqua as kq', function ($join) use ($made) {
                $join->on('ctn.manguoidung', '=', 'kq.manguoidung')
                    ->where('kq.made', '=', $made);
            })
            ->where('gdt.made', $made)
            ->where('gdt.manhom', $manhom)
            ->select([
                'ctn.manguoidung',
                'u.hoten',
                'u.email',
                'kq.makq',
                'kq.diemthi',
                'kq.thoigianvaothi',
                'kq.thoigianlambai',
                'kq.socaudung',
                'kq.solanchuyentab',
            ])
            ->distinct()
            ->orderBy('ctn.manguoidung')
            ->get();

        return response()->json($rows);
    }

    /**
     * API chi tiết bài làm 
     */
    public function getResultDetail(Request $request, int $makq)
    {
        $kq = KetQuaModel::query()->with(['dethi'])->findOrFail($makq);
        $test = $kq->dethi;
        if (!$test) abort(404);

        $userId = (string) $request->user()->id;
        $isTeacher = (string) $test->nguoitao === $userId;
        $isOwnerStudent = (string) $kq->manguoidung === $userId;

        if (!$isTeacher) {
            // SV: phải là chủ bài + đã có điểm + đề cho xem bài làm
            if (!$isOwnerStudent || $kq->diemthi === null || (int) $test->hienthibailam !== 1) {
                abort(403);
            }
        }

        $detail = ChiTietKetQuaModel::query()
            ->where('makq', $makq)
            ->with(['cauhoi.cautraloi'])
            ->get()
            ->map(function ($row) use ($test, $isTeacher) {
                $q = $row->cauhoi;
                $answers = ($q?->cautraloi ?? collect())->map(function ($a) use ($test, $row, $isTeacher) {
                    $isCorrect = (int) $a->ladapan === 1;
                    $hideCorrect = (!$isTeacher && (int) $test->xemdapan !== 1);
                    return [
                        'macautl' => $a->macautl,
                        'noidungtl' => $a->noidungtl,
                        'ladapan' => $hideCorrect ? null : $isCorrect,
                        'is_chosen' => ((int) $row->dapanchon === (int) $a->macautl),
                    ];
                });

                // đáp án đúng (để hiện "Đáp án đúng: ..." khi SV chọn sai)
                $correctIds = ($q?->cautraloi ?? collect())->filter(fn ($a) => (int) $a->ladapan === 1)->pluck('macautl')->values();
                $hideCorrect = (!$isTeacher && (int) $test->xemdapan !== 1);

                return [
                    'macauhoi' => $q?->macauhoi,
                    'noidung' => $q?->noidung,
                    'dokho' => $q?->dokho,
                    'dapanchon' => $row->dapanchon,
                    'correct_ids' => $hideCorrect ? [] : $correctIds,
                    'cautraloi' => $answers,
                ];
            })
            ->values();

        return response()->json([
            'makq' => $kq->makq,
            'made' => $kq->made,
            'manguoidung' => $kq->manguoidung,
            'diemthi' => $kq->diemthi,
            'xemdapan' => (int) $test->xemdapan,
            'hienthibailam' => (int) $test->hienthibailam,
            'questions' => $detail,
        ]);
    }

    private function validateTest(Request $request): array
    {
        return $request->validate([
            'monthi' => ['required', 'string', 'max:20', 'exists:monhoc,mamonhoc'],
            'tende' => ['required', 'string', 'max:255'],
            'thoigianthi' => ['required', 'integer', 'min:1', 'max:600'],
            'thoigianbatdau' => ['nullable', 'date'],
            'thoigianketthuc' => ['nullable', 'date', 'after_or_equal:thoigianbatdau'],
            'hienthibailam' => ['nullable', 'boolean'],
            'xemdiemthi' => ['nullable', 'boolean'],
            'xemdapan' => ['nullable', 'boolean'],
            'troncauhoi' => ['nullable', 'boolean'],
            'trondapan' => ['nullable', 'boolean'],
            'nopbaichuyentab' => ['nullable', 'boolean'],
            'loaide' => ['nullable', 'integer', 'in:0,1'],
            'socaude' => ['nullable', 'integer', 'min:0'],
            'socautb' => ['nullable', 'integer', 'min:0'],
            'socaukho' => ['nullable', 'integer', 'min:0'],
            'trangthai' => ['nullable', 'boolean'],
        ]);
    }

    private function validateNhomIds(Request $request): array
    {
        $nhom = $request->input('nhom_ids', []);
        if (!is_array($nhom)) $nhom = [];

        // chỉ nhận số nguyên dương
        $nhom = array_values(array_filter(array_map(function ($v) {
            return filter_var($v, FILTER_VALIDATE_INT) ? (int) $v : null;
        }, $nhom)));

        return $nhom;
    }

    private function validateChuongIds(Request $request): array
    {
        $chuong = $request->input('chuong_ids', []);
        if (!is_array($chuong)) $chuong = [];

        $chuong = array_values(array_filter(array_map(function ($v) {
            return filter_var($v, FILTER_VALIDATE_INT) ? (int) $v : null;
        }, $chuong)));

        return $chuong;
    }

    private function syncGiaoDeThi(int $made, array $nhomIds): void
    {
        DB::table('giaodethi')->where('made', $made)->delete();
        foreach ($nhomIds as $manhom) {
            DB::table('giaodethi')->insert(['made' => $made, 'manhom' => $manhom]);
        }
    }

    private function syncDeThiTuDong(int $made, array $chuongIds): void
    {
        DB::table('dethitudong')->where('made', $made)->delete();
        foreach ($chuongIds as $machuong) {
            DB::table('dethitudong')->insert(['made' => $made, 'machuong' => $machuong]);
        }
    }

    private function generateAutoQuestionsOrFail(DeThiModel $test, array $chuongIds): void
    {
        $needEasy = (int) ($test->socaude ?? 0);
        $needMed  = (int) ($test->socautb ?? 0);
        $needHard = (int) ($test->socaukho ?? 0);
        $totalNeed = $needEasy + $needMed + $needHard;

        if ($totalNeed <= 0) {
            throw ValidationException::withMessages([
                'socaude' => 'Vui lòng nhập số câu cho đề tự động (dễ/trung bình/khó).',
            ]);
        }
        if (empty($chuongIds)) {
            throw ValidationException::withMessages([
                'chuong_ids' => 'Vui lòng chọn ít nhất 1 chương cho đề tự động.',
            ]);
        }

        $pick = function (int $level, int $limit) use ($test, $chuongIds) {
            if ($limit <= 0) return collect();
            return CauHoiModel::query()
                ->active()
                ->where('mamonhoc', $test->monthi)
                ->whereIn('machuong', $chuongIds)
                ->where('dokho', $level)
                ->inRandomOrder()
                ->limit($limit)
                ->pluck('macauhoi');
        };

        $easy = $pick(1, $needEasy);
        $med  = $pick(2, $needMed);
        $hard = $pick(3, $needHard);

        if ($easy->count() < $needEasy || $med->count() < $needMed || $hard->count() < $needHard) {
            throw ValidationException::withMessages([
                'chuong_ids' => 'Không đủ câu hỏi trong các chương đã chọn để tạo đề tự động theo số lượng/độ khó.',
            ]);
        }

        $ids = $easy->merge($med)->merge($hard)->values();

        ChiTietDeThiModel::where('made', $test->made)->delete();
        $thutu = 1;
        foreach ($ids as $qid) {
            ChiTietDeThiModel::create([
                'made' => $test->made,
                'macauhoi' => (int) $qid,
                'thutu' => $thutu++,
            ]);
        }
    }

    private function checkStudentAllowed(string $userId, int $made): bool
    {
        $nhomIds = DB::table('chitietnhom')
            ->where('manguoidung', $userId)
            ->pluck('manhom');

        if ($nhomIds->isEmpty()) return false;

        return DB::table('giaodethi')
            ->where('made', $made)
            ->whereIn('manhom', $nhomIds)
            ->exists();
    }
}

