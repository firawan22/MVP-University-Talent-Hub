import { RecommendationsService } from './recommendations.service';
export declare class RecommendationsController {
    private readonly svc;
    constructor(svc: RecommendationsService);
    recommendOpportunities(user: any): Promise<any[]>;
    recommendSkills(user: any): Promise<any[]>;
}
