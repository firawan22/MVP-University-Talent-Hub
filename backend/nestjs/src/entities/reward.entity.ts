import { Entity, PrimaryGeneratedColumn, Column } from 'typeorm';

@Entity({ name: 'rewards' })
export class RewardEntity {
  @PrimaryGeneratedColumn()
  id: number;

  @Column()
  name: string;

  @Column({ default: 0 })
  pointsRequired: number;

  @Column({ nullable: true })
  description: string;
}
