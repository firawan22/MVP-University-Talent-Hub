import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { NotificationEntity } from '../entities/notification.entity';

@Injectable()
export class NotificationsService {
  constructor(
    @InjectRepository(NotificationEntity) private repo: Repository<NotificationEntity>,
  ) {}

  async create(userId: number, title: string, message?: string, link?: string) {
    return this.repo.save(this.repo.create({ userId, title, message, link }));
  }

  findByUser(userId: number) {
    return this.repo.find({
      where: { userId },
      order: { createdAt: 'DESC' },
      take: 50,
    });
  }

  async markAsRead(id: number, userId: number) {
    return this.repo.update({ id, userId }, { isRead: true });
  }

  async getUnreadCount(userId: number) {
    return this.repo.count({ where: { userId, isRead: false } });
  }

  async markAllAsRead(userId: number) {
    return this.repo.update({ userId, isRead: false }, { isRead: true });
  }
}
