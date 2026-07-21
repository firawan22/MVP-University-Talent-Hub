import { SubmissionsService } from './submissions.service';
import { SubmissionEntity } from '../entities/submission.entity';
import type { UserProfile } from '../app.service';
export declare class SubmissionsController {
    private readonly submissionsService;
    constructor(submissionsService: SubmissionsService);
    getSubmissions(): Promise<SubmissionEntity[]>;
    createSubmission(body: {
        title: string;
        description: string;
        evidence?: string;
    }, user: UserProfile): Promise<SubmissionEntity>;
    reviewSubmission(id: string, decision: 'approved' | 'rejected'): Promise<SubmissionEntity | null>;
}
