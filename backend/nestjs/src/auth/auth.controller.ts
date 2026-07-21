import { Body, Controller, Post } from '@nestjs/common';
import { AuthService } from './auth.service';

class LoginDto {
  email: string;
  password: string;
}

class RegisterDto {
  email: string;
  password: string;
  name: string;
  role?: string;
}

@Controller('auth')
export class AuthController {
  constructor(private readonly authService: AuthService) {}

  @Post('login')
  async login(@Body() body: LoginDto) {
    const result = await this.authService.login(body.email, body.password);
    return result ?? { error: 'Invalid credentials' };
  }

  @Post('register')
  async register(@Body() body: RegisterDto) {
    try {
      const result = await this.authService.register(body.email, body.password, body.name, body.role);
      return result ?? { error: 'Registration failed' };
    } catch (e: any) {
      return { error: e.message || 'Registration failed' };
    }
  }
}
