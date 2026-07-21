import { Entity, PrimaryGeneratedColumn, Column } from 'typeorm';

@Entity({ name: 'submissions' })
export class SubmissionEntity {
  @PrimaryGeneratedColumn()
  id: number;

  @Column()
  studentId: number;

  @Column({ nullable: true })
  title: string;

  @Column()
  description: string;

  @Column({ nullable: true })
  evidence: string;

  @Column({ default: 'project' })
  submissionType: string;

  @Column({ default: 'pending' })
  status: 'pending' | 'approved' | 'rejected';

  @Column({ default: 0 })
  pointsAwarded: number;
}
