import { NextRequest, NextResponse } from 'next/server'
import { getServerSession } from 'next-auth'
import { authOptions } from '@/lib/auth'
import { prisma } from '@/lib/prisma'

// GET /api/candidates/[id] - Get single candidate
export async function GET(
  request: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    const session = await getServerSession(authOptions)

    if (!session) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const candidate = await prisma.candidate.findUnique({
      where: { id: parseInt(params.id) },
      include: {
        user: {
          select: {
            id: true,
            name: true,
            email: true,
          },
        },
        files: true,
        statuses: {
          orderBy: { createdAt: 'desc' },
        },
        comments: {
          orderBy: { createdAt: 'desc' },
        },
        gallupReports: true,
        gallupTalents: {
          orderBy: { talentRank: 'asc' },
        },
      },
    })

    if (!candidate) {
      return NextResponse.json({ error: 'Candidate not found' }, { status: 404 })
    }

    return NextResponse.json({
      success: true,
      data: candidate,
    })
  } catch (error) {
    console.error('Error fetching candidate:', error)
    return NextResponse.json(
      { error: 'Failed to fetch candidate' },
      { status: 500 }
    )
  }
}

// PATCH /api/candidates/[id] - Update candidate
export async function PATCH(
  request: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    const session = await getServerSession(authOptions)

    if (!session) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const body = await request.json()
    const candidateId = parseInt(params.id)

    // Check if candidate exists
    const existingCandidate = await prisma.candidate.findUnique({
      where: { id: candidateId },
    })

    if (!existingCandidate) {
      return NextResponse.json({ error: 'Candidate not found' }, { status: 404 })
    }

    // Track changes for history
    const changes: any[] = []
    for (const [key, value] of Object.entries(body)) {
      if ((existingCandidate as any)[key] !== value) {
        changes.push({
          candidateId,
          field: key,
          oldValue: JSON.stringify((existingCandidate as any)[key]),
          newValue: JSON.stringify(value),
          changedBy: parseInt(session.user.id),
        })
      }
    }

    // Update candidate and create history records
    const [candidate] = await Promise.all([
      prisma.candidate.update({
        where: { id: candidateId },
        data: {
          ...body,
          birthDate: body.birth_date ? new Date(body.birth_date) : undefined,
        },
      }),
      changes.length > 0
        ? prisma.candidateHistory.createMany({
            data: changes,
          })
        : Promise.resolve(),
    ])

    return NextResponse.json({
      success: true,
      data: candidate,
    })
  } catch (error) {
    console.error('Error updating candidate:', error)
    return NextResponse.json(
      { error: 'Failed to update candidate' },
      { status: 500 }
    )
  }
}

// DELETE /api/candidates/[id] - Delete candidate
export async function DELETE(
  request: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    const session = await getServerSession(authOptions)

    if (!session || !session.user.isAdmin) {
      return NextResponse.json({ error: 'Forbidden' }, { status: 403 })
    }

    await prisma.candidate.delete({
      where: { id: parseInt(params.id) },
    })

    return NextResponse.json({
      success: true,
      message: 'Candidate deleted successfully',
    })
  } catch (error) {
    console.error('Error deleting candidate:', error)
    return NextResponse.json(
      { error: 'Failed to delete candidate' },
      { status: 500 }
    )
  }
}
