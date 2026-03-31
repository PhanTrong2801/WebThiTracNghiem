<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserModel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => Auth::user(), // Truyền thông tin user hiện tại
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $request->validate([
            'hoten' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'ngaysinh' => 'nullable|string',
            'gioitinh' => 'nullable|integer',
        ]);

        $userModel = new UserModel();
        
        // Kiểm tra email mới có trùng với người khác không
        if ($request->email !== Auth::user()->email) {
            if ($userModel->checkEmailExist($request->email, $userId)) {
                return Redirect::back()->withErrors(['email' => 'Địa chỉ email đã tồn tại !']);
            }
        }

        $userModel->updateProfile(
            $request->hoten,
            $request->gioitinh ?? 1,
            $request->ngaysinh,
            $request->email,
            $userId
        );

        return Redirect::route('profile.edit')->with('status', 'Cập nhật hồ sơ thành công!');
    }

    /**
     * Thay đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $userId = Auth::id();
        $userModel = new UserModel();

        // Kiểm tra mật khẩu cũ
        if (!$userModel->checkPassword($userId, $request->current_password)) {
            return Redirect::back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $userModel->changePassword($userId, $request->new_password);

        return Redirect::route('profile.edit')->with('status', 'Thay đổi mật khẩu thành công!');
    }

    /**
     * Tải lên ảnh đại diện
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'file-img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $userId = Auth::id();
        $file = $request->file('file-img');
        $imageExtension = strtolower($file->getClientOriginalExtension());
        $validImageExtension = ['jpg', 'jpeg', 'png', 'gif'];
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $userModel = new UserModel();
        $result = $userModel->uploadAvatar($userId, $file->getRealPath(), $imageExtension, $validImageExtension, $name);

        if ($result) {
            return Redirect::route('profile.edit')->with('status', 'Cập nhật avatar thành công!');
        }

        return Redirect::back()->withErrors(['file-img' => 'Định dạng file không hợp lệ.']);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Lấy thông tin role của user hiện tại
     */
    public function getRole()
    {
        $user = Auth::user();
        $roles = $user->getRolePermissions();
        return response()->json($roles);
    }
}
