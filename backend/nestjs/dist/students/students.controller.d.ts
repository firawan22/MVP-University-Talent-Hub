import { StudentsService } from './students.service';
export declare class StudentsController {
    private readonly svc;
    constructor(svc: StudentsService);
    getAll(): Promise<import("../entities/student.entity").StudentEntity[]>;
    search(q: string): Promise<import("../entities/student.entity").StudentEntity[]>;
    getOne(id: string): Promise<import("../entities/student.entity").StudentEntity | null>;
    create(body: any): Promise<import("../entities/student.entity").StudentEntity[]>;
    update(id: string, body: any): Promise<import("../entities/student.entity").StudentEntity | null>;
    remove(id: string): Promise<import("typeorm").DeleteResult>;
}
