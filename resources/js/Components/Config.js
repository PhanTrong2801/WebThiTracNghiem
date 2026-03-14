// resources/js/constants/navbarConfig.js
export const NAVBAR_MENU = [
    {
        'name'  : 'Dashboard',
        'icon'  : 'fa fa-rocket',
        'url'   : 'dashboard'
    },
    {
        'name': 'Sinh viên',
        'type'  : 'heading',
        'navbarItem' : [
            {
                'name'  : 'Học phần',
                'icon'  : 'fa fa-users-line',
                'url'   : 'client/group',
                'role' : 'tghocphan'
    },
            {
                'name'  : 'Đề thi',
                'icon'  : 'fa fa-graduation-cap',
                'url'   : 'client/test',
                'role' : 'tgthi'
            },
        ]
            },
    {
        'name'  : 'Quản lý',
        'type'  : 'heading',
        'navbarItem' : [
            {
                'name'  : 'Nhóm học phần',
                'icon'  : 'fa fa-layer-group',
                'url'   : 'module',
                'role' : 'hocphan'
            },
            {
                'name'  : 'Câu hỏi',
                'icon'  : 'fa fa-circle-question',
                'url'   : 'question',
                'role' : 'cauhoi'
            },
            {
                'name'  : 'Người dùng',
                'icon'  : 'fa fa-user-friends',
                'url'   : 'users',
                'role' : 'nguoidung'
            },
            {
                'name'  : 'Môn học',
                'icon'  : 'fa fa-folder',
                'url'   : 'subject',
                'role' : 'monhoc'
            },
            {
                'name'  : 'Chương',
                'icon'  : 'fa fa-list-ol',
                'url'   : 'chapter',
                'role' : 'chuong'
            },
            {
                'name'  : 'Phân công',
                'icon'  : 'fa fa-person-harassing',
                'url'   : 'assignment',
                'role' : 'phancong'
            },
            {
                'name'  : 'Đề kiểm tra',
                'icon'  : 'fa fa-file',
                'url'   : 'test',
                'role' : 'dethi'
            },
            {
                'name'  : 'Thông báo',
                'icon'  : 'fa fa-comment',
                'url'   : 'teacher_announcement',
                'role' : 'thongbao'
            },
        ]
    },
    {
        'name'  : 'Quản trị',
        'type'  : 'heading',
        'navbarItem' : [
            {
                'name'  : 'Nhóm quyền',
                'icon'  : 'fa fa-users-gear',
                'url'   : 'roles',
                'role' : 'nhomquyen'
            },
            // {
            //     'name'  : 'Cài đặt',
            //     'icon'  : 'fa fa-gears',
            //     'url'   : 'setting',
            //     'role' : 'caidat'
            // }
        ]
    }
];