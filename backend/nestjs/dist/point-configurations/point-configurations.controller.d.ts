import { PointConfigurationsService } from './point-configurations.service';
export declare class PointConfigurationsController {
    private readonly svc;
    constructor(svc: PointConfigurationsService);
    getAll(): Promise<import("../entities/point-configuration.entity").PointConfigurationEntity[]>;
    update(id: string, body: {
        points: number;
    }): Promise<import("../entities/point-configuration.entity").PointConfigurationEntity | null>;
}
