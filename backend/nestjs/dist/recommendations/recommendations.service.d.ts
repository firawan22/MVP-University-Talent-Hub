import { Repository } from 'typeorm';
import { StudentEntity } from '../entities/student.entity';
import { OpportunityEntity } from '../entities/opportunity.entity';
export declare class RecommendationsService {
    private studentRepo;
    private oppRepo;
    constructor(studentRepo: Repository<StudentEntity>, oppRepo: Repository<OpportunityEntity>);
    recommendOpportunities(studentId: number): Promise<any[]>;
    recommendSkills(studentId: number): Promise<any[]>;
}
