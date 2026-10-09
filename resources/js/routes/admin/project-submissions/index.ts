import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin/project-submissions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/project-submissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin/project-submissions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin/project-submissions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin/project-submissions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin/project-submissions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin/project-submissions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\AdminWorkspaceController::index
* @see app/Http/Controllers/Admin/AdminWorkspaceController.php:34
* @route '/admin/project-submissions'
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

/**
* @see \App\Http\Controllers\Admin\ProjectSubmissionController::update
* @see app/Http/Controllers/Admin/ProjectSubmissionController.php:198
* @route '/admin/project-submissions/{userProject}'
*/
export const update = (args: { userProject: number | { id: number } } | [userProject: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/admin/project-submissions/{userProject}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Admin\ProjectSubmissionController::update
* @see app/Http/Controllers/Admin/ProjectSubmissionController.php:198
* @route '/admin/project-submissions/{userProject}'
*/
update.url = (args: { userProject: number | { id: number } } | [userProject: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { userProject: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { userProject: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            userProject: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        userProject: typeof args.userProject === 'object'
        ? args.userProject.id
        : args.userProject,
    }

    return update.definition.url
            .replace('{userProject}', parsedArgs.userProject.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ProjectSubmissionController::update
* @see app/Http/Controllers/Admin/ProjectSubmissionController.php:198
* @route '/admin/project-submissions/{userProject}'
*/
update.patch = (args: { userProject: number | { id: number } } | [userProject: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\ProjectSubmissionController::update
* @see app/Http/Controllers/Admin/ProjectSubmissionController.php:198
* @route '/admin/project-submissions/{userProject}'
*/
const updateForm = (args: { userProject: number | { id: number } } | [userProject: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\ProjectSubmissionController::update
* @see app/Http/Controllers/Admin/ProjectSubmissionController.php:198
* @route '/admin/project-submissions/{userProject}'
*/
updateForm.patch = (args: { userProject: number | { id: number } } | [userProject: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const projectSubmissions = {
    index: Object.assign(index, index),
    update: Object.assign(update, update),
}

export default projectSubmissions