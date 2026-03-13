import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';

export default function GuestLayout({ children }) {
    return (
        <div className="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0">
            <div className="flex justify-center">
                <Link href="/">
                    <img src="/img/logo_truong.png" alt="Logo trường" className="max-w-xs sm:max-w-md w-full h-auto mx-auto mb-2" style={{maxWidth: '350px'}} />
                </Link>
            </div>

            <div className="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
                {children}
            </div>
        </div>
    );
}
