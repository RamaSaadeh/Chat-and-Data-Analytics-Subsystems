package com.example.KnowledgeProductivity.task;

import org.springframework.stereotype.Service;

@Service
public class TaskService {

    private final TaskRepository taskRepository;

    public TaskService(TaskRepository taskRepository) {
        this.taskRepository = taskRepository;
    }

    public Long getNoOfTasks() {
        return taskRepository.count();
    }


}
