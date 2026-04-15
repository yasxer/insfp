import api from '../axios'

export const teacherHomeworkApi = {
    getHomeworks() {
        return api.get('/api/teacher/homeworks')
    },
    createHomework(data) {
        return api.post('/api/teacher/homeworks', data, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
    },
    getHomeworkDetails(id) {
        return api.get(`/api/teacher/homeworks/${id}`)
    },
    gradeSubmission(homeworkId, submissionId, data) {
        return api.post(`/api/teacher/homeworks/${homeworkId}/submissions/${submissionId}/grade`, data)
    }
}

export const studentHomeworkApi = {
    getHomeworks() {
        return api.get('/api/student/homeworks')
    },
    submitHomework(id, data) {
        return api.post(`/api/student/homeworks/${id}/submit`, data, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
    }
}

