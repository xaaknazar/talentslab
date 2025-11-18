import { Candidate, User, GallupReport, GallupTalent, GardnerTestResult } from '@prisma/client'

export type CandidateWithRelations = Candidate & {
  user?: User | null
  gallupReports?: GallupReport[]
  gallupTalents?: GallupTalent[]
}

export type UserWithCandidate = User & {
  candidate?: Candidate | null
  gardnerTestResult?: GardnerTestResult | null
}

export interface FamilyMember {
  type: string
  relation: string
  birth_year: string
  profession?: string
  name?: string
}

export interface FamilyStructured {
  parents: Array<{
    relation: string
    birth_year: string
    profession?: string
  }>
  siblings: Array<{
    relation: string
    birth_year: string
  }>
  children: Array<{
    name: string
    birth_year: string
  }>
  is_new_structure: boolean
}

export interface University {
  name: string
  degree: string
  field: string
  graduation_year: string
}

export interface LanguageSkill {
  language: string
  level: string
}

export interface WorkExperience {
  company: string
  position: string
  start_date: string
  end_date?: string
  description?: string
}

export interface CandidateFormData {
  // Step 1: Basic Information
  full_name?: string
  patronymic?: string
  email: string
  phone: string
  gender: string
  marital_status: string
  birth_date: string
  birth_place: string
  current_city: string
  ready_to_relocate?: boolean
  instagram?: string
  photo?: string

  // Step 2: Additional Information
  religion?: string
  is_practicing?: boolean
  family_members?: FamilyMember[]
  hobbies?: string
  interests?: string
  visited_countries?: string[]
  books_per_year?: string
  favorite_sports?: string[]
  entertainment_hours_weekly?: number
  educational_hours_weekly?: number
  social_media_hours_weekly?: number
  has_driving_license?: boolean

  // Step 3: Education and Work
  school: string
  universities?: University[]
  language_skills?: LanguageSkill[]
  computer_skills?: string
  work_experience?: WorkExperience[]
  total_experience_years?: number
  job_satisfaction?: number
  desired_position: string
  activity_sphere?: string
  expected_salary: number
  employer_requirements?: string

  // Step 4: Tests
  gallup_pdf?: string
  mbti_type?: string
}

export interface ApiResponse<T = any> {
  success: boolean
  data?: T
  error?: string
  message?: string
}

export interface PaginationParams {
  page?: number
  limit?: number
  sortBy?: string
  sortOrder?: 'asc' | 'desc'
}

export interface PaginatedResponse<T> {
  data: T[]
  pagination: {
    total: number
    page: number
    limit: number
    totalPages: number
  }
}

export type MBTIType =
  | 'INTJ' | 'INTP' | 'ENTJ' | 'ENTP'
  | 'INFJ' | 'INFP' | 'ENFJ' | 'ENFP'
  | 'ISTJ' | 'ISFJ' | 'ESTJ' | 'ESFJ'
  | 'ISTP' | 'ISFP' | 'ESTP' | 'ESFP'

export type CandidateStatus = 'draft' | 'submitted' | 'reviewing' | 'approved' | 'rejected'

export type GallupReportType = 'Individual Report' | 'Team Report' | 'Leadership Report'
