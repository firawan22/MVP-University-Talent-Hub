import { Body, Controller, Delete, Get, Headers, Param, Patch, Post, Put, Query, UseGuards } from '@nestjs/common';
import { AppService } from './app.service';
import type { StudentProfile, UserProfile } from './app.service';
import { AuthGuard } from './auth/auth.guard';
import { Roles } from './auth/roles.decorator';
import { RolesGuard } from './auth/roles.guard';
import { User } from './auth/user.decorator';

@Controller()
export class AppController {
  constructor(private readonly appService: AppService) {}

  @Get()
  getHello(): string {
    return 'University Talent Hub API is running';
  }

  @Get('health')
  getHealth() {
    return { status: 'ok', service: 'University Talent Hub API' };
  }

  @Get('dashboard')
  getDashboard() {
    return this.appService.getDashboardStats();
  }

  @Get('me/profile')
  @UseGuards(AuthGuard)
  getMyProfile(@User() user: UserProfile) {
    return this.appService.getMyProfile(user.id);
  }

  @Put('me/profile')
  @UseGuards(AuthGuard)
  updateMyProfile(
    @Body() body: Partial<StudentProfile> & { skills?: string | string[]; certificates?: string | string[]; portfolios?: string | string[] },
    @User() user: UserProfile,
  ) {
    return this.appService.updateMyProfile(user.id, body);
  }


  @Get('rewards')
  async getRewards() {
    return this.appService.getRewards();
  }

  @Post('rewards')
  @UseGuards(AuthGuard, RolesGuard)
  @Roles('admin')
  createReward(@Body() body: { name: string; description: string; pointsRequired: number }) {
    return this.appService.createReward(body);
  }

  @Put('rewards/:id')
  @UseGuards(AuthGuard, RolesGuard)
  @Roles('admin')
  updateReward(@Param('id') id: string, @Body() body: { name?: string; description?: string; pointsRequired?: number }) {
    return this.appService.updateReward(Number(id), body);
  }

  @Delete('rewards/:id')
  @UseGuards(AuthGuard, RolesGuard)
  @Roles('admin')
  deleteReward(@Param('id') id: string) {
    return this.appService.deleteReward(Number(id));
  }

  @Post('rewards/:id/redeem')
  async redeemReward(@Param('id') id: string, @Query('studentId') studentId: string, @Headers('authorization') authorization?: string) {
    const token = authorization?.startsWith('Bearer ') ? authorization.slice(7) : authorization;
    const user = token ? await this.appService.verifyToken(token) : null;
    if (!user) {
      return { error: 'Unauthorized' };
    }

    // students can redeem for themselves; admins can redeem on behalf
    const sid = user.role === 'admin' ? Number(studentId) : user.id;

    return this.appService.redeemReward(sid, Number(id));
  }

  @Get('leaderboard')
  getLeaderboard() {
    return this.appService.getLeaderboard();
  }
}
