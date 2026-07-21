import { Controller, Get, UseGuards } from '@nestjs/common';
import { RecommendationsService } from './recommendations.service';
import { AuthGuard } from '../auth/auth.guard';
import { User } from '../auth/user.decorator';

@Controller('recommendations')
@UseGuards(AuthGuard)
export class RecommendationsController {
  constructor(private readonly svc: RecommendationsService) {}

  @Get('opportunities')
  recommendOpportunities(@User() user: any) {
    return this.svc.recommendOpportunities(user.id);
  }

  @Get('skills')
  recommendSkills(@User() user: any) {
    return this.svc.recommendSkills(user.id);
  }
}
