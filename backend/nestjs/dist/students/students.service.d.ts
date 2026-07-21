import { Repository } from 'typeorm';
import { StudentEntity } from '../entities/student.entity';
export declare class StudentsService {
    private repo;
    constructor(repo: Repository<StudentEntity>);
    findAll(): Promise<StudentEntity[]>;
    search(query: string): Promise<StudentEntity[]>;
    findOne(id: number): Promise<StudentEntity | null>;
    create(data: Partial<StudentEntity>): Promise<StudentEntity[]>;
    update(id: number, data: Partial<StudentEntity>): Promise<StudentEntity | null>;
    remove(id: number): Promise<import("typeorm").DeleteResult>;
}
