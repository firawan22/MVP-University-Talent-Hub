import { Injectable, OnModuleInit } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { PointConfigurationEntity } from '../entities/point-configuration.entity';

@Injectable()
export class PointConfigurationsService implements OnModuleInit {
  constructor(
    @InjectRepository(PointConfigurationEntity) private repo: Repository<PointConfigurationEntity>,
  ) {}

  async onModuleInit() {
    const count = await this.repo.count();
    if (count === 0) {
      const defaults = [
        { type: 'project', points: 50 },
        { type: 'certificate', points: 30 },
        { type: 'competition', points: 100 },
        { type: 'internship', points: 200 },
      ];
      await this.repo.save(defaults.map((d) => this.repo.create(d)));
    }
  }

  findAll() {
    return this.repo.find();
  }

  async update(id: number, points: number) {
    await this.repo.update(id, { points });
    return this.repo.findOne({ where: { id } });
  }
}
