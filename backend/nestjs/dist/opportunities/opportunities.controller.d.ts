import { OpportunitiesService } from './opportunities.service';
export declare class OpportunitiesController {
    private readonly svc;
    constructor(svc: OpportunitiesService);
    getAll(): Promise<import("../entities/opportunity.entity").OpportunityEntity[]>;
    getOne(id: string): Promise<import("../entities/opportunity.entity").OpportunityEntity | null>;
    create(body: any): Promise<import("../entities/opportunity.entity").OpportunityEntity>;
    update(id: string, body: any): Promise<import("../entities/opportunity.entity").OpportunityEntity | null>;
    remove(id: string): Promise<import("typeorm").DeleteResult>;
}
