import UserManagementController from './UserManagementController'
import EvaluationSubmissionController from './EvaluationSubmissionController'
import FeedbackController from './FeedbackController'
import AssessmentQuestionController from './AssessmentQuestionController'

const Admin = {
    UserManagementController: Object.assign(UserManagementController, UserManagementController),
    EvaluationSubmissionController: Object.assign(EvaluationSubmissionController, EvaluationSubmissionController),
    FeedbackController: Object.assign(FeedbackController, FeedbackController),
    AssessmentQuestionController: Object.assign(AssessmentQuestionController, AssessmentQuestionController),
}

export default Admin