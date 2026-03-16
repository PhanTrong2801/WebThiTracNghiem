<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'starts_with:DH'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Nhận id thay vì email
        $id = $this->input('id');
        $password = $this->input('password');

        // 2. Tìm User để kiểm tra trạng thái khóa (giống code cũ của bạn)
        $user = \App\Models\UserModel::find($id);

        if (!$user) {
            // Quăng lỗi nếu không tồn tại
            throw ValidationException::withMessages([
                'id' => 'Tài khoản không tồn tại!',
            ]);
        }

        if ($user->trangthai == 0) {
            // Quăng lỗi nếu bị khóa
            throw ValidationException::withMessages([
                'id' => 'Tài khoản đã bị khóa!',
            ]);
        }

        // 3. Thực hiện Auth::attempt() dựa vào cột id (vì id đang lưu ở id)
        $remember = $this->boolean('remember') || $this->input('remember') === true || $this->input('remember') === 'on';
if (! Auth::attempt(['id' => $id, 'password' => $password], $remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'id' => 'Sai mật khẩu!', // Báo lỗi tài khoản / mật khẩu sai
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }


    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
