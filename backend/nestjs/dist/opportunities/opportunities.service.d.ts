import { Repository } from 'typeorm';
import { OpportunityEntity } from '../entities/opportunity.entity';
export declare class OpportunitiesService {
    private repo;
    constructor(repo: Repository<OpportunityEntity>);
    findAll(): Promise<OpportunityEntity[]>;
    findOne(id: number): Promise<OpportunityEntity | null>;
    create(data: Partial<OpportunityEntity>): Promise<OpportunityEntity>;
    update(id: number, data: Partial<OpportunityEntity>): Promise<OpportunityEntity | null>;
    remove(id: number): Promise<import("typeorm").DeleteResult>;
}
