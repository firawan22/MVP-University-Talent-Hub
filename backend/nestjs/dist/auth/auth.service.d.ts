import { Repository } from 'typeorm';
import { UserEntity } from '../entities/user.entity';
import { StudentEntity } from '../entities/student.entity';
import { AppService } from '../app.service';
export declare class AuthService {
    private usersRepo;
    private studentRepo;
    private readonly appService;
    constructor(usersRepo: Repository<UserEntity>, studentRepo: Repository<StudentEntity>, appService: AppService);
    validateUser(email: string, password: string): Promise<UserEntity | null>;
    login(email: string, password: string): Promise<{
        token: string;
        user: {
            id: number;
            name: string;
            email: string;
            role: string;
            points: number;
        };
    } | null>;
    register(email: string, password: string, name: string, role?: string): Promise<{
        token: string;
        user: {
            id: number;
            name: string;
            email: string;
            role: string;
            points: number;
        };
    }>;
}
