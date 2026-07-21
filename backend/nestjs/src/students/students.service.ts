import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { StudentEntity } from '../entities/student.entity';

@Injectable()
export class StudentsService {
  constructor(@InjectRepository(StudentEntity) private repo: Repository<StudentEntity>) {}

  findAll() {
    return this.repo.find();
  }

  async search(query: string) {
    if (!query || query.trim() === '') {
      return this.repo.find();
    }
    const q = `%${query}%`;
    return this.repo.createQueryBuilder('student')
      .where('student.name LIKE :q', { q })
      .orWhere('student.major LIKE :q', { q })
      .orWhere('student.skills LIKE :q', { q })
      .getMany();
  }

  findOne(id: number) {
    return this.repo.findOne({ where: { id } });
  }

  create(data: Partial<StudentEntity>) {
    const ent = this.repo.create(data as any);
    return this.repo.save(ent);
  }

  async update(id: number, data: Partial<StudentEntity>) {
    await this.repo.update(id, data as any);
    return this.findOne(id);
  }

  remove(id: number) {
    return this.repo.delete(id);
  }
}
