import { OnModuleInit } from '@nestjs/common';
import { Repository } from 'typeorm';
import { PointConfigurationEntity } from '../entities/point-configuration.entity';
export declare class PointConfigurationsService implements OnModuleInit {
    private repo;
    constructor(repo: Repository<PointConfigurationEntity>);
    onModuleInit(): Promise<void>;
    findAll(): Promise<PointConfigurationEntity[]>;
    update(id: number, points: number): Promise<PointConfigurationEntity | null>;
}
