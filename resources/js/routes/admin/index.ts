import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import users from './users'
import submissions from './submissions'
import projectSubmissions from './project-submissions'
import feedback from './feedback'
import careers from './careers'
import skills from './skills'
import prerequisites from './prerequisites'
import assessments from './assessments'
import questions from './questions'
import materials from './materials'
import projects from './projects'
/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:14
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
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:14
* @route '/admin/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:14
* @route '/admin/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:14
* @route '/admin/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:14
* @route '/admin/dashboard'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:14
* @route '/admin/dashboard'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::dashboard
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:14
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
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const admin = {
    users: Object.assign(users, users),
    dashboard: Object.assign(dashboard, dashboard),
    index: Object.assign(index, index),
    submissions: Object.assign(submissions, submissions),
    projectSubmissions: Object.assign(projectSubmissions, projectSubmissions),
    feedback: Object.assign(feedback, feedback),
    careers: Object.assign(careers, careers),
    skills: Object.assign(skills, skills),
    prerequisites: Object.assign(prerequisites, prerequisites),
    assessments: Object.assign(assessments, assessments),
    questions: Object.assign(questions, questions),
    materials: Object.assign(materials, materials),
    projects: Object.assign(projects, projects),
}

export default admin