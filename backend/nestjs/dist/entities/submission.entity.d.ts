export declare class SubmissionEntity {
    id: number;
    studentId: number;
    title: string;
    description: string;
    evidence: string;
    submissionType: string;
    status: 'pending' | 'approved' | 'rejected';
    pointsAwarded: number;
}
