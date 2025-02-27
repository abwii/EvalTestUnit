describe("Tests End-to-End : Gestion des utilisateurs", () => {
    const user = {
      name: "John Doe",
      email: "john.doe@example.com",
    };
  
    const updatedUser = {
      name: "John Doe Updated",
      email: "john.doe.updated@example.com",
    };
  
    beforeEach(() => {
      cy.visit("http://localhost/exam/Exo1/src/");
    });
  
    it("Ajoute un utilisateur", () => {
      cy.get('input[id="name"]').type(user.name);
      cy.get('input[id="email"]').type(user.email);
      cy.get('button[type="submit"]').click();
      cy.get("ul#userList").should("contain", user.name);
      cy.get("ul#userList").should("contain", user.email);
    });
  
    it("Vérifie que l'utilisateur ajouté est bien affiché dans la liste", () => {
      cy.get("ul#userList").should("contain", user.name);
      cy.get("ul#userList").should("contain", user.email);
    });
  
    it("Modifie un utilisateur", () => {
      cy.contains(user.name).parent().find("button").contains("✏️").click();
      cy.get('input[id="name"]').clear().type(updatedUser.name);
      cy.get('input[id="email"]').clear().type(updatedUser.email);
      cy.get('button[type="submit"]').click();
      cy.get("ul#userList").should("contain", updatedUser.name);
      cy.get("ul#userList").should("contain", updatedUser.email);
    });
  
    it("Supprime un utilisateur", () => {
      cy.contains(updatedUser.name)
        .parent()
        .find("button")
        .contains("❌")
        .click();
      cy.get("ul#userList").should("not.contain", updatedUser.name);
      cy.get("ul#userList").should("not.contain", updatedUser.email);
    });
  });