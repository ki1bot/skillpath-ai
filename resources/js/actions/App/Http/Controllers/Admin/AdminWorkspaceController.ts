import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:20
* @route '/admin/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/admin/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:20
* @route '/admin/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:20
* @route '/admin/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:20
* @route '/admin/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:20
* @route '/admin/dashboard'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:20
* @route '/admin/dashboard'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:20
* @route '/admin/dashboard'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyDashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:132
* @route '/admin'
*/
export const legacyDashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: legacyDashboard.url(options),
    method: 'get',
})

legacyDashboard.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyDashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:132
* @route '/admin'
*/
legacyDashboard.url = (options?: RouteQueryOptions) => {
    return legacyDashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyDashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:132
* @route '/admin'
*/
legacyDashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: legacyDashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyDashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:132
* @route '/admin'
*/
legacyDashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: legacyDashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyDashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:132
* @route '/admin'
*/
const legacyDashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: legacyDashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyDashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:132
* @route '/admin'
*/
legacyDashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: legacyDashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyDashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:132
* @route '/admin'
*/
legacyDashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: legacyDashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

legacyDashboard.form = legacyDashboardForm

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::submissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:121
* @route '/admin/submissions'
*/
export const submissions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: submissions.url(options),
    method: 'get',
})

submissions.definition = {
    methods: ["get","head"],
    url: '/admin/submissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::submissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:121
* @route '/admin/submissions'
*/
submissions.url = (options?: RouteQueryOptions) => {
    return submissions.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::submissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:121
* @route '/admin/submissions'
*/
submissions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: submissions.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::submissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:121
* @route '/admin/submissions'
*/
submissions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: submissions.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::submissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:121
* @route '/admin/submissions'
*/
const submissionsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: submissions.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::submissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:121
* @route '/admin/submissions'
*/
submissionsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: submissions.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::submissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:121
* @route '/admin/submissions'
*/
submissionsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: submissions.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

submissions.form = submissionsForm

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyProjectSubmissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:137
* @route '/admin/project-submissions'
*/
export const legacyProjectSubmissions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: legacyProjectSubmissions.url(options),
    method: 'get',
})

legacyProjectSubmissions.definition = {
    methods: ["get","head"],
    url: '/admin/project-submissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyProjectSubmissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:137
* @route '/admin/project-submissions'
*/
legacyProjectSubmissions.url = (options?: RouteQueryOptions) => {
    return legacyProjectSubmissions.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyProjectSubmissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:137
* @route '/admin/project-submissions'
*/
legacyProjectSubmissions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: legacyProjectSubmissions.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyProjectSubmissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:137
* @route '/admin/project-submissions'
*/
legacyProjectSubmissions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: legacyProjectSubmissions.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyProjectSubmissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:137
* @route '/admin/project-submissions'
*/
const legacyProjectSubmissionsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: legacyProjectSubmissions.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyProjectSubmissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:137
* @route '/admin/project-submissions'
*/
legacyProjectSubmissionsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: legacyProjectSubmissions.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::legacyProjectSubmissions
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:137
* @route '/admin/project-submissions'
*/
legacyProjectSubmissionsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: legacyProjectSubmissions.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

legacyProjectSubmissions.form = legacyProjectSubmissionsForm

const AdminWorkspaceController = { dashboard, legacyDashboard, submissions, legacyProjectSubmissions }

export default AdminWorkspaceController