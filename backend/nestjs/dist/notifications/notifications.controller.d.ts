import { NotificationsService } from './notifications.service';
export declare class NotificationsController {
    private readonly svc;
    constructor(svc: NotificationsService);
    getAll(user: any): Promise<import("../entities/notification.entity").NotificationEntity[]>;
    getUnreadCount(user: any): Promise<number>;
    markAsRead(id: string, user: any): Promise<import("typeorm").UpdateResult>;
    markAllAsRead(user: any): Promise<import("typeorm").UpdateResult>;
}
