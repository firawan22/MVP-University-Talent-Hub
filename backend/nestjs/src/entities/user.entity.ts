import { Entity, PrimaryGeneratedColumn, Column } from 'typeorm';

@Entity({ name: 'users' })
export class UserEntity {
  @PrimaryGeneratedColumn()
  id: number;

  @Column({ unique: true })
  email: string;

  @Column()
  name: string;

  @Column({ default: 'student' })
  role: string;

  @Column({ default: 0 })
  points: number;

  @Column({ nullable: true })
  passwordHash: string;
}
