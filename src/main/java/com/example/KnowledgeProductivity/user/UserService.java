package com.example.KnowledgeProductivity.user;

import jakarta.transaction.Transactional;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Optional;

@Service
public class UserService implements UserDetailsService {

    private final UserRepository studentRepository;

    @Autowired
    public UserService(UserRepository studentRepository) {
        this.studentRepository = studentRepository;
    }

    public List<User> getStudents() {
        return studentRepository.findAll();
    }

    public void addNewStudent(User student) {
        Optional<User> studentByEmail = studentRepository.findStudentByEmail(student.getEmail());

        if (studentByEmail.isPresent()) {
            throw new IllegalStateException("email taken");
        }

        studentRepository.save(student);
    }

    public void deleteStudent(Long studentId) {
       boolean exists = studentRepository.existsById(studentId);

       if (!exists) {
           throw new IllegalStateException("student does not exist");
       }
       studentRepository.deleteById(studentId);
    }

    @Transactional
    public void updateStudent(Long studentId, User name, String email) {
    }


    @Override
    public UserDetails loadUserByUsername(String username) throws UsernameNotFoundException {
        Optional<User> user =  studentRepository.findByFname(username);

        if (user.isPresent()) {
            var userObj = user.get();
           return org.springframework.security.core.userdetails.User.builder()
                    .username(userObj.getFname())
                    .password(userObj.getPassword())
                    .build();
        }
        else{
            throw new UsernameNotFoundException("username not found");
        }
    }

    public List<User> getContacts(Long userId) {
        System.out.println(studentRepository.findAll());
        return studentRepository.findAll();
    }
}
