import UserManagementController from './UserManagementController'
import AdminWorkspaceController from './AdminWorkspaceController'
import EvaluationSubmissionController from './EvaluationSubmissionController'
import ProjectSubmissionController from './ProjectSubmissionController'
import FeedbackController from './FeedbackController'
import AssessmentQuestionController from './AssessmentQuestionController'

const Admin = {
    UserManagementController: Object.assign(UserManagementController, UserManagementController),
    AdminWorkspaceController: Object.assign(AdminWorkspaceController, AdminWorkspaceController),
    EvaluationSubmissionController: Object.assign(EvaluationSubmissionController, EvaluationSubmissionController),
    ProjectSubmissionController: Object.assign(ProjectSubmissionController, ProjectSubmissionController),
    FeedbackController: Object.assign(FeedbackController, FeedbackController),
    AssessmentQuestionController: Object.assign(AssessmentQuestionController, AssessmentQuestionController),
}

export default Admin