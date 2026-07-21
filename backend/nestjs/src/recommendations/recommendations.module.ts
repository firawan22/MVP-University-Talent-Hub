import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { RecommendationsController } from './recommendations.controller';
import { RecommendationsService } from './recommendations.service';
import { StudentEntity } from '../entities/student.entity';
import { OpportunityEntity } from '../entities/opportunity.entity';

@Module({
  imports: [TypeOrmModule.forFeature([StudentEntity, OpportunityEntity])],
  controllers: [RecommendationsController],
  providers: [RecommendationsService],
  exports: [RecommendationsService],
})
export class RecommendationsModule {}
