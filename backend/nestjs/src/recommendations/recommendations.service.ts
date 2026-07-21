import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { StudentEntity } from '../entities/student.entity';
import { OpportunityEntity } from '../entities/opportunity.entity';

@Injectable()
export class RecommendationsService {
  constructor(
    @InjectRepository(StudentEntity) private studentRepo: Repository<StudentEntity>,
    @InjectRepository(OpportunityEntity) private oppRepo: Repository<OpportunityEntity>,
  ) {}

  async recommendOpportunities(studentId: number): Promise<any[]> {
    const student = await this.studentRepo.findOne({ where: { id: studentId } });
    if (!student || !student.skills || student.skills.length === 0) {
      const all = await this.oppRepo.find({ where: { isActive: true }, take: 10 });
      return all.map((o) => ({ ...o, relevanceScore: 0 }));
    }

    const opportunities = await this.oppRepo.find({ where: { isActive: true } });
    const studentSkills = (student.skills || []).map((s) => s.toLowerCase());

    const scored = opportunities.map((opp) => {
      const oppText = (opp.title + ' ' + opp.description + ' ' + (opp.company || '')).toLowerCase();
      const matchCount = studentSkills.filter((skill) => oppText.includes(skill)).length;
      return { ...opp, relevanceScore: matchCount };
    });

    return scored.sort((a, b) => b.relevanceScore - a.relevanceScore).slice(0, 10);
  }

  async recommendSkills(studentId: number): Promise<any[]> {
    const student = await this.studentRepo.findOne({ where: { id: studentId } });
    if (!student || !student.skills || student.skills.length === 0) {
      return [];
    }

    const allStudents = await this.studentRepo.find();
    const studentSkillsLower = student.skills.map((s) => s.toLowerCase());

    const peerSkills = new Map<string, number>();
    for (const peer of allStudents) {
      if (peer.id === studentId || !peer.skills) continue;
      const peerSkillsLower = peer.skills.map((s) => s.toLowerCase());
      const hasMatch = peerSkillsLower.some((s) => studentSkillsLower.includes(s));
      if (hasMatch) {
        for (const skill of peerSkillsLower) {
          if (!studentSkillsLower.includes(skill)) {
            peerSkills.set(skill, (peerSkills.get(skill) || 0) + 1);
          }
        }
      }
    }

    return [...peerSkills.entries()]
      .sort((a, b) => b[1] - a[1])
      .slice(0, 5)
      .map(([skill, count]) => ({ skill, peerCount: count }));
  }
}
