import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { OpportunityEntity } from '../entities/opportunity.entity';

@Injectable()
export class OpportunitiesService {
  constructor(
    @InjectRepository(OpportunityEntity) private repo: Repository<OpportunityEntity>,
  ) {}

  findAll() {
    return this.repo.find({ where: { isActive: true }, order: { createdAt: 'DESC' } });
  }

  findOne(id: number) {
    return this.repo.findOne({ where: { id } });
  }

  create(data: Partial<OpportunityEntity>) {
    return this.repo.save(this.repo.create(data));
  }

  async update(id: number, data: Partial<OpportunityEntity>) {
    await this.repo.update(id, data);
    return this.findOne(id);
  }

  remove(id: number) {
    return this.repo.delete(id);
  }
}
