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
public class CustomUserService implements UserDetailsService {

    private final CustomUserRepository studentRepository;

    @Autowired
    public CustomUserService(CustomUserRepository studentRepository) {
        this.studentRepository = studentRepository;
    }

    public List<CustomUser> getStudents() {
        return studentRepository.findAll();
    }

    public void addNewStudent(CustomUser student) {
        Optional<CustomUser> studentByEmail = studentRepository.findStudentByEmail(student.getEmail());

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
    public void updateStudent(Long studentId, CustomUser name, String email) {
    }


    @Override
    public UserDetails loadUserByUsername(String username) throws UsernameNotFoundException {
        return studentRepository.findByFname(username)
                .orElseThrow(() -> new UsernameNotFoundException("User not found with email: " + username));
    }

    public List<CustomUser> getContacts(Long userId) {
        System.out.println(studentRepository.findAll());
        return studentRepository.findAll();
    }
}
