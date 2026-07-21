import { CanActivate, ExecutionContext, Injectable } from '@nestjs/common';
import { Request } from 'express';
import { AppService } from '../app.service';

@Injectable()
export class AuthGuard implements CanActivate {
  constructor(private readonly appService: AppService) {}

  async canActivate(context: ExecutionContext): Promise<boolean> {
    const req = context.switchToHttp().getRequest<Request & { user?: any }>();
    const auth = req.headers['authorization'] || '';
    const token = typeof auth === 'string' && auth.startsWith('Bearer ') ? auth.slice(7) : auth as string;
    if (!token) return false;
    const user = await this.appService.verifyToken(token);
    if (!user) return false;
    req.user = user;
    return true;
  }
}
