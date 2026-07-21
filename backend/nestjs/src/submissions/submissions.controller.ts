import { Body, Controller, Get, Param, Patch, Post, Query, UseGuards } from '@nestjs/common';
import { SubmissionsService } from './submissions.service';
import { SubmissionEntity } from '../entities/submission.entity';
import { AuthGuard } from '../auth/auth.guard';
import { RolesGuard } from '../auth/roles.guard';
import { Roles } from '../auth/roles.decorator';
import { User } from '../auth/user.decorator';
import type { UserProfile } from '../app.service';

@Controller('submissions')
export class SubmissionsController {
  constructor(private readonly submissionsService: SubmissionsService) {}

  @Get()
  @UseGuards(AuthGuard)
  getSubmissions(): Promise<SubmissionEntity[]> {
    return this.submissionsService.getAll();
  }

  @Post()
  @UseGuards(AuthGuard)
  createSubmission(@Body() body: { title: string; description: string; evidence?: string }, @User() user: UserProfile) {
    return this.submissionsService.createSubmission(user.id, body);
  }

  @Patch(':id')
  @UseGuards(AuthGuard, RolesGuard)
  @Roles('admin')
  async reviewSubmission(
    @Param('id') id: string,
    @Query('decision') decision: 'approved' | 'rejected',
  ) {
    return this.submissionsService.reviewSubmission(Number(id), decision);
  }
}
