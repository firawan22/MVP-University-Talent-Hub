import { Entity, PrimaryGeneratedColumn, Column } from 'typeorm';

@Entity({ name: 'point_configurations' })
export class PointConfigurationEntity {
  @PrimaryGeneratedColumn()
  id: number;

  @Column({ unique: true })
  type: string;

  @Column({ default: 50 })
  points: number;
}
