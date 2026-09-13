import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\AssessmentController::show
* @see app/Http/Controllers/AssessmentController.php:15
* @route '/assessment'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/assessment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AssessmentController::show
* @see app/Http/Controllers/AssessmentController.php:15
* @route '/assessment'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssessmentController::show
* @see app/Http/Controllers/AssessmentController.php:15
* @route '/assessment'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AssessmentController::show
* @see app/Http/Controllers/AssessmentController.php:15
* @route '/assessment'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AssessmentController::show
* @see app/Http/Controllers/AssessmentController.php:15
* @route '/assessment'
*/
const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AssessmentController::show
* @see app/Http/Controllers/AssessmentController.php:15
* @route '/assessment'
*/
showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AssessmentController::show
* @see app/Http/Controllers/AssessmentController.php:15
* @route '/assessment'
*/
showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\AssessmentController::start
* @see app/Http/Controllers/AssessmentController.php:24
* @route '/assessment/start'
*/
export const start = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: start.url(options),
    method: 'post',
})

start.definition = {
    methods: ["post"],
    url: '/assessment/start',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AssessmentController::start
* @see app/Http/Controllers/AssessmentController.php:24
* @route '/assessment/start'
*/
start.url = (options?: RouteQueryOptions) => {
    return start.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssessmentController::start
* @see app/Http/Controllers/AssessmentController.php:24
* @route '/assessment/start'
*/
start.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: start.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AssessmentController::start
* @see app/Http/Controllers/AssessmentController.php:24
* @route '/assessment/start'
*/
const startForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: start.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AssessmentController::start
* @see app/Http/Controllers/AssessmentController.php:24
* @route '/assessment/start'
*/
startForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: start.url(options),
    method: 'post',
})

start.form = startForm

/**
* @see \App\Http\Controllers\AssessmentController::abandon
* @see app/Http/Controllers/AssessmentController.php:33
* @route '/assessment/abandon'
*/
export const abandon = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: abandon.url(options),
    method: 'post',
})

abandon.definition = {
    methods: ["post"],
    url: '/assessment/abandon',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AssessmentController::abandon
* @see app/Http/Controllers/AssessmentController.php:33
* @route '/assessment/abandon'
*/
abandon.url = (options?: RouteQueryOptions) => {
    return abandon.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssessmentController::abandon
* @see app/Http/Controllers/AssessmentController.php:33
* @route '/assessment/abandon'
*/
abandon.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: abandon.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AssessmentController::abandon
* @see app/Http/Controllers/AssessmentController.php:33
* @route '/assessment/abandon'
*/
const abandonForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: abandon.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AssessmentController::abandon
* @see app/Http/Controllers/AssessmentController.php:33
* @route '/assessment/abandon'
*/
abandonForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: abandon.url(options),
    method: 'post',
})

abandon.form = abandonForm

/**
* @see \App\Http\Controllers\AssessmentController::submit
* @see app/Http/Controllers/AssessmentController.php:42
* @route '/assessment'
*/
export const submit = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/assessment',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AssessmentController::submit
* @see app/Http/Controllers/AssessmentController.php:42
* @route '/assessment'
*/
submit.url = (options?: RouteQueryOptions) => {
    return submit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssessmentController::submit
* @see app/Http/Controllers/AssessmentController.php:42
* @route '/assessment'
*/
submit.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AssessmentController::submit
* @see app/Http/Controllers/AssessmentController.php:42
* @route '/assessment'
*/
const submitForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submit.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AssessmentController::submit
* @see app/Http/Controllers/AssessmentController.php:42
* @route '/assessment'
*/
submitForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submit.url(options),
    method: 'post',
})

submit.form = submitForm

const assessment = {
    show: Object.assign(show, show),
    start: Object.assign(start, start),
    abandon: Object.assign(abandon, abandon),
    submit: Object.assign(submit, submit),
}

export default assessment