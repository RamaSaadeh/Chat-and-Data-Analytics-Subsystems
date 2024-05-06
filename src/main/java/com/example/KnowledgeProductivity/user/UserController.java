package com.example.KnowledgeProductivity.user;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;


import java.util.List;

@RestController
public class UserController
{


    private final UserService studentService;

    @Autowired
    public UserController(UserService studentService) {
        this.studentService = studentService;
    }

    @GetMapping
    public List<User> getStudents() {
        return studentService.getStudents();
    }

    @PostMapping
    public void registerNewStudent(@RequestBody User student) {
        studentService.addNewStudent(student);
    }

    @PutMapping("{studentId}")
    public void updateStudent(@PathVariable("studentId") Long studentId,
                              @RequestParam(required = false) User name,
                              @RequestParam(required = false) String email) {
        studentService.updateStudent(studentId, name, email);
    }

    @DeleteMapping(path = "{studentId}")
    public void deleteStudent(@PathVariable("studentId") Long studentId) {
        studentService.deleteStudent(studentId);
    }
}
