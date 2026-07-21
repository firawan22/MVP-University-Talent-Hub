import { Test, TestingModule } from '@nestjs/testing';
import { getRepositoryToken } from '@nestjs/typeorm';
import { AppController } from './app.controller';
import { AppService } from './app.service';
import { UserEntity } from './entities/user.entity';
import { StudentEntity } from './entities/student.entity';
import { SubmissionEntity } from './entities/submission.entity';
import { RewardEntity } from './entities/reward.entity';

describe('AppController', () => {
  let appController: AppController;

  const mockRepo = { find: jest.fn().mockResolvedValue([]), findOne: jest.fn().mockResolvedValue(null), count: jest.fn().mockResolvedValue(0) };

  beforeEach(async () => {
    const app: TestingModule = await Test.createTestingModule({
      controllers: [AppController],
      providers: [
        AppService,
        { provide: getRepositoryToken(UserEntity), useValue: { ...mockRepo } },
        { provide: getRepositoryToken(StudentEntity), useValue: { ...mockRepo } },
        { provide: getRepositoryToken(SubmissionEntity), useValue: { ...mockRepo } },
        { provide: getRepositoryToken(RewardEntity), useValue: { ...mockRepo } },
      ],
    }).compile();

    appController = app.get<AppController>(AppController);
  });

  describe('root', () => {
    it('should return "Hello World!"', () => {
      expect(appController.getHello()).toBe('University Talent Hub API is running');
    });
  });
});
