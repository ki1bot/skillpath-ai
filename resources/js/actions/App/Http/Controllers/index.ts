import PublicPageController from './PublicPageController'
import SessionHeartbeatController from './SessionHeartbeatController'
import DashboardController from './DashboardController'
import OnboardingController from './OnboardingController'
import AssessmentController from './AssessmentController'
import SkillGapController from './SkillGapController'
import RoadmapController from './RoadmapController'
import ProjectController from './ProjectController'
import ProgressController from './ProgressController'
import FeedbackController from './FeedbackController'
import Admin from './Admin'
import AdminDashboardController from './AdminDashboardController'
import AdminController from './AdminController'
import Settings from './Settings'
const Controllers = {
    PublicPageController: Object.assign(PublicPageController, PublicPageController),
SessionHeartbeatController: Object.assign(SessionHeartbeatController, SessionHeartbeatController),
DashboardController: Object.assign(DashboardController, DashboardController),
OnboardingController: Object.assign(OnboardingController, OnboardingController),
AssessmentController: Object.assign(AssessmentController, AssessmentController),
SkillGapController: Object.assign(SkillGapController, SkillGapController),
RoadmapController: Object.assign(RoadmapController, RoadmapController),
ProjectController: Object.assign(ProjectController, ProjectController),
ProgressController: Object.assign(ProgressController, ProgressController),
FeedbackController: Object.assign(FeedbackController, FeedbackController),
Admin: Object.assign(Admin, Admin),
AdminDashboardController: Object.assign(AdminDashboardController, AdminDashboardController),
AdminController: Object.assign(AdminController, AdminController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers