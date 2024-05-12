package com.example.KnowledgeProductivity.auth;

public class RegisterRequest {

    private String Fname;
    private String Email;
    private String Password;

    public RegisterRequest(String fname, String email, String password) {
        Fname = fname;
        Email = email;
        Password = password;
    }

    public String getFname() {
        return Fname;
    }

    public void setFname(String fname) {
        Fname = fname;
    }

    public String getEmail() {
        return Email;
    }

    public void setEmail(String email) {
        Email = email;
    }

    public String getPassword() {
        return Password;
    }

    public void setPassword(String password) {
        Password = password;
    }

    public RegisterRequest() {
    }
}
