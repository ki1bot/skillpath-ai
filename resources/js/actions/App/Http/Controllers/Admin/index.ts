import UserManagementController from './UserManagementController'
import FeedbackController from './FeedbackController'
import AssessmentQuestionController from './AssessmentQuestionController'
const Admin = {
    UserManagementController: Object.assign(UserManagementController, UserManagementController),
FeedbackController: Object.assign(FeedbackController, FeedbackController),
AssessmentQuestionController: Object.assign(AssessmentQuestionController, AssessmentQuestionController),
}

export default Admin