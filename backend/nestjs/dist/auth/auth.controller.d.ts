import { AuthService } from './auth.service';
declare class LoginDto {
    email: string;
    password: string;
}
declare class RegisterDto {
    email: string;
    password: string;
    name: string;
    role?: string;
}
export declare class AuthController {
    private readonly authService;
    constructor(authService: AuthService);
    login(body: LoginDto): Promise<{
        token: string;
        user: {
            id: number;
            name: string;
            email: string;
            role: string;
            points: number;
        };
    } | {
        error: string;
    }>;
    register(body: RegisterDto): Promise<{
        token: string;
        user: {
            id: number;
            name: string;
            email: string;
            role: string;
            points: number;
        };
    } | {
        error: any;
    }>;
}
export {};
