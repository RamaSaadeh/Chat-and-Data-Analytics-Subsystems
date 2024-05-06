package com.example.KnowledgeProductivity.user;


import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
public interface UserRepository extends JpaRepository<User, Long> {

    Optional<User> findStudentByEmail(String email);

   Optional <User> findByFname(String username);

   @Override
   List<User> findAll();
}
