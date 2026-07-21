import { CanActivate, ExecutionContext } from '@nestjs/common';
import { AppService } from '../app.service';
export declare class AuthGuard implements CanActivate {
    private readonly appService;
    constructor(appService: AppService);
    canActivate(context: ExecutionContext): Promise<boolean>;
}
