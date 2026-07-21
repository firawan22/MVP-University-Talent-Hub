import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { PointConfigurationsController } from './point-configurations.controller';
import { PointConfigurationsService } from './point-configurations.service';
import { PointConfigurationEntity } from '../entities/point-configuration.entity';

@Module({
  imports: [TypeOrmModule.forFeature([PointConfigurationEntity])],
  controllers: [PointConfigurationsController],
  providers: [PointConfigurationsService],
  exports: [PointConfigurationsService],
})
export class PointConfigurationsModule {}
