import { Repository } from 'typeorm';
import { NotificationEntity } from '../entities/notification.entity';
export declare class NotificationsService {
    private repo;
    constructor(repo: Repository<NotificationEntity>);
    create(userId: number, title: string, message?: string, link?: string): Promise<NotificationEntity>;
    findByUser(userId: number): Promise<NotificationEntity[]>;
    markAsRead(id: number, userId: number): Promise<import("typeorm").UpdateResult>;
    getUnreadCount(userId: number): Promise<number>;
    markAllAsRead(userId: number): Promise<import("typeorm").UpdateResult>;
}
