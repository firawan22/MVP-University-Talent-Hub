import { SubmissionsService } from './submissions.service';

describe('SubmissionsService', () => {
  let service: SubmissionsService;
  const mockSubmission = { id: 1, studentId: 2, description: 'x', title: 'Test', submissionType: 'project', status: 'pending', pointsAwarded: 0 } as any;
  const mockStudent = { id: 2, points: 0 } as any;

  const mockPointConfig = { id: 1, type: 'project', points: 50 };

  beforeEach(() => {
    const submissionsRepo: any = {
      findOne: jest.fn().mockResolvedValue(mockSubmission),
      find: jest.fn().mockResolvedValue([]),
      save: jest.fn().mockImplementation((s) => Promise.resolve(s)),
    };
    const usersRepo: any = {
      findOne: jest.fn().mockResolvedValue(mockStudent),
      find: jest.fn().mockResolvedValue([]),
      save: jest.fn().mockImplementation((u) => Promise.resolve(u)),
    };
    const studentRepo: any = {
      findOne: jest.fn().mockResolvedValue(mockStudent),
      save: jest.fn().mockImplementation((s) => Promise.resolve(s)),
    };
    const pointConfigRepo: any = {
      findOne: jest.fn().mockResolvedValue(mockPointConfig),
    };
    const notificationRepo: any = {
      save: jest.fn().mockImplementation((n) => Promise.resolve(n)),
    };

    service = new SubmissionsService(submissionsRepo, usersRepo, studentRepo, pointConfigRepo, notificationRepo);
  });

  it('approves a submission and awards points to the student', async () => {
    const submission = await service.reviewSubmission(1, 'approved');

    expect(submission!.status).toBe('approved');
    expect(submission!.pointsAwarded).toBe(50);
  });

  it('rejects a submission and leaves points at zero', async () => {
    const submission = await service.reviewSubmission(2, 'rejected');

    expect(submission!.status).toBe('rejected');
    expect(submission!.pointsAwarded).toBe(0);
  });
});
