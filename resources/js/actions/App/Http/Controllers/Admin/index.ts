import UserManagementController from './UserManagementController'
import AdminWorkspaceController from './AdminWorkspaceController'
import EvaluationSubmissionController from './EvaluationSubmissionController'
import ProjectSubmissionController from './ProjectSubmissionController'
import FeedbackController from './FeedbackController'
import CareerContentController from './CareerContentController'
import AssessmentQuestionController from './AssessmentQuestionController'

const Admin = {
    UserManagementController: Object.assign(UserManagementController, UserManagementController),
    AdminWorkspaceController: Object.assign(AdminWorkspaceController, AdminWorkspaceController),
    EvaluationSubmissionController: Object.assign(EvaluationSubmissionController, EvaluationSubmissionController),
    ProjectSubmissionController: Object.assign(ProjectSubmissionController, ProjectSubmissionController),
    FeedbackController: Object.assign(FeedbackController, FeedbackController),
    CareerContentController: Object.assign(CareerContentController, CareerContentController),
    AssessmentQuestionController: Object.assign(AssessmentQuestionController, AssessmentQuestionController),
}

export default Admin