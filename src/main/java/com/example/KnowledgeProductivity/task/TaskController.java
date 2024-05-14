package com.example.KnowledgeProductivity.task;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
public class TaskController {

    private final TaskService taskService;

    public TaskController(TaskService taskService) {
        this.taskService = taskService;
    }

    @GetMapping("/dashboard/task")
    public Long getNoOfTask(){

        return taskService.getNoOfTasks();
    }

}
