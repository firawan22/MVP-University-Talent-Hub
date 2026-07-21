import { Controller, Get, Param, Patch, UseGuards } from '@nestjs/common';
import { NotificationsService } from './notifications.service';
import { AuthGuard } from '../auth/auth.guard';
import { User } from '../auth/user.decorator';

@Controller('notifications')
@UseGuards(AuthGuard)
export class NotificationsController {
  constructor(private readonly svc: NotificationsService) {}

  @Get()
  getAll(@User() user: any) {
    return this.svc.findByUser(user.id);
  }

  @Get('unread-count')
  getUnreadCount(@User() user: any) {
    return this.svc.getUnreadCount(user.id);
  }

  @Patch(':id/read')
  markAsRead(@Param('id') id: string, @User() user: any) {
    return this.svc.markAsRead(Number(id), user.id);
  }

  @Patch('read-all')
  markAllAsRead(@User() user: any) {
    return this.svc.markAllAsRead(user.id);
  }
}
