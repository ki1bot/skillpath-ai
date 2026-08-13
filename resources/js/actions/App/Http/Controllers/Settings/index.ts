import ProfileController from './ProfileController'
import EmailVerificationController from './EmailVerificationController'
import SecurityController from './SecurityController'
const Settings = {
    ProfileController: Object.assign(ProfileController, ProfileController),
EmailVerificationController: Object.assign(EmailVerificationController, EmailVerificationController),
SecurityController: Object.assign(SecurityController, SecurityController),
}

export default Settings